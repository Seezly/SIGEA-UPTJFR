import { type TermSection } from '@/types';
import ReactMarkdown from 'react-markdown';

interface DocsProps {
    sections: TermSection[];
    updatedAt: string | null;
}

export function Docs({ sections, updatedAt }: DocsProps) {
    return (
        <section id="docs" className="flex w-full flex-col items-center justify-center px-4 py-8">
            <div className="flex w-full max-w-6xl flex-col-reverse lg:flex-row-reverse lg:items-start lg:justify-between">
                <div className="flex w-full flex-col items-start justify-start gap-8 lg:max-w-3xl">
                    <div className="flex w-full flex-col gap-4">
                        {sections.map((section: TermSection, index: number) => {
                            return (
                                <>
                                    <article id={section.id} className="border-muted flex flex-col gap-4 border-b pb-4" key={section.id}>
                                        <h3>
                                            {index < 10 ? `0${index + 1}` : index + 1}. {section.title}
                                        </h3>
                                        <ReactMarkdown>{section.content}</ReactMarkdown>
                                    </article>
                                </>
                            );
                        })}
                    </div>
                    <p>Última actualización: {updatedAt ?? 'Nunca'}</p>
                </div>
                <aside className="bg-background-darker w-full p-4 lg:max-w-xs">
                    <h3>En esta página</h3>
                    <ol className="flex flex-col gap-2">
                        {sections.map((section: TermSection, index: number) => {
                            return (
                                <li key={section.id}>
                                    <span className="text-secondary-text mr-2">{index < 10 ? `0${index + 1}` : index + 1}</span>
                                    <a href={`#${section.id}`}>{section.title}</a>
                                </li>
                            );
                        })}
                    </ol>
                </aside>
            </div>
        </section>
    );
}
